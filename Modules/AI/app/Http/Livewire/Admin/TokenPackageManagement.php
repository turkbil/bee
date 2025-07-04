<?php

namespace Modules\AI\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Modules\AI\App\Models\AITokenPackage;

class TokenPackageManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'sort_order';
    public $sortDirection = 'asc';
    public $selectedItems = [];
    public $selectAll = false;
    public $perPage = 10;
    public $showOnlineOnly = false;
    public $activeTab = 'list';
    public $editMode = false;
    public $editingPackage = null;
    
    // Package form fields
    public $features = [];
    public $name = '';
    public $description = '';
    public $token_amount = '';
    public $price = '';
    public $currency = 'TRY';
    public $is_active = true;
    public $is_popular = false;
    public $sort_order = 0;
    
    // Modal properties
    public $showDeleteModal = false;
    public $deleteId = null;
    public $deleteTitle = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'sort_order'],
        'sortDirection' => ['except' => 'asc']
    ];

    protected $listeners = ['update-package-order' => 'updatePackageOrder'];

    public function render()
    {
        $packages = AITokenPackage::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->showOnlineOnly, function ($query) {
                $query->where('is_active', true);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('ai::admin.livewire.token-package-management', compact('packages'))
            ->layout('admin.layout', [
                'pretitle' => 'AI Token Yönetimi',
                'title' => 'Token Paketleri'
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

    public function createPackage()
    {
        return redirect()->route('admin.ai.tokens.packages.admin');
    }

    public function editPackage($id)
    {
        return redirect()->route('admin.ai.tokens.packages.admin')->with('edit_package_id', $id);
    }

    public function toggleActive($id)
    {
        $package = AITokenPackage::findOrFail($id);
        $package->update(['is_active' => !$package->is_active]);
        
        $status = $package->is_active ? 'aktif' : 'pasif';
        session()->flash('success', "Paket {$status} hale getirildi.");
    }

    public function togglePopular($id)
    {
        $package = AITokenPackage::findOrFail($id);
        $package->update(['is_popular' => !$package->is_popular]);
        
        $status = $package->is_popular ? 'popüler' : 'normal';
        session()->flash('success', "Paket {$status} olarak işaretlendi.");
    }

    public function confirmDelete($id)
    {
        $package = AITokenPackage::findOrFail($id);
        $this->showDeleteModal($id, $package->name);
    }

    public function updatePackageOrder($event)
    {
        if (isset($event['packages']) && is_array($event['packages'])) {
            foreach ($event['packages'] as $package) {
                AITokenPackage::where('id', $package['id'])
                    ->update(['sort_order' => $package['order']]);
            }

            session()->flash('success', 'Paket sıralaması güncellendi.');
        }
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedItems = [];
        } else {
            $this->selectedItems = AITokenPackage::pluck('id')->toArray();
        }
        $this->selectAll = !$this->selectAll;
    }

    public function bulkToggleActive($status)
    {
        if (empty($this->selectedItems)) {
            return;
        }

        AITokenPackage::whereIn('id', $this->selectedItems)
            ->update(['is_active' => $status]);

        $statusText = $status ? 'aktif' : 'pasif';
        $count = count($this->selectedItems);
        
        session()->flash('success', "{$count} paket {$statusText} hale getirildi.");

        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        
        if ($tab === 'manage') {
            $this->editMode = false;
            $this->editingPackage = null;
        }
    }

    public function mount()
    {
        // URL'den edit parametresi kontrol et
        if (session()->has('edit_package_id')) {
            $packageId = session()->pull('edit_package_id');
            $this->editingPackage = AITokenPackage::find($packageId);
            if ($this->editingPackage) {
                $this->activeTab = 'manage';
                $this->editMode = true;
            }
        }
    }

    public function showDeleteModal($id, $title = '')
    {
        $this->deleteId = $id;
        $this->deleteTitle = $title;
        $this->showDeleteModal = true;
    }

    public function hideDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->deleteTitle = '';
    }

    public function deletePackage()
    {
        if ($this->deleteId) {
            $package = AITokenPackage::find($this->deleteId);
            if ($package) {
                // Satın alma kontrolü
                if ($package->purchases()->exists()) {
                    session()->flash('error', 'Bu paketten satın alma yapılmış, silinemez.');
                    $this->hideDeleteModal();
                    return;
                }
                
                $package->delete();
                session()->flash('success', 'Paket başarıyla silindi.');
            }
        }
        
        $this->hideDeleteModal();
        $this->resetPage();
    }
}