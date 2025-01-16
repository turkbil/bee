<?php
namespace App\Livewire\Modals;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BulkDeleteModal extends Component
{
    public $showModal = false;
    public $module;
    public $selectedItems = [];

    protected $listeners = ['showBulkDeleteModal'];

    public function mount()
    {
        $this->showModal = false;
    }

    public function showBulkDeleteModal($params)
    {
        $this->module = $params['module'];
        $this->selectedItems = $params['selectedItems'];
        $this->showModal = true;
    }

    public function bulkDelete()
    {
        try {
            DB::beginTransaction();

            $tenant = tenancy()->tenant;

            if (!$tenant || empty($this->selectedItems)) {
                $this->dispatch('toast', [
                    'title'   => 'Hata!',
                    'message' => 'İşlem yapılamadı.',
                    'type'    => 'error',
                ]);
                return;
            }

            $modelClass = "Modules\\" . ucfirst($this->module) . "\\App\\Models\\" . ucfirst($this->module);
            $items = $modelClass::where('tenant_id', $tenant->id)
                ->whereIn($this->module . '_id', $this->selectedItems)
                ->get();

            foreach ($items as $item) {
                log_activity(
                    ucfirst($this->module),
                    "\"{$item->title}\" toplu silme işleminde silindi.",
                    $item,
                    [],
                    'silindi'
                );
            }

            $modelClass::where('tenant_id', $tenant->id)
                ->whereIn($this->module . '_id', $this->selectedItems)
                ->delete();

            DB::commit();

            $this->showModal = false;

            $this->dispatch('toast', [
                'title'   => 'Silindi!',
                'message' => count($this->selectedItems) . " adet kayıt silindi.",
                'type'    => 'danger',
            ]);

            // Silme işleminden sonra ana componente bildir
            $this->dispatch('bulkItemsDeleted')->to($this->module . '-component');
            $this->dispatch('resetSelectAll')->to($this->module . '-component');

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('toast', [
                'title'   => 'Hata!',
                'message' => 'Silme işlemi sırasında bir hata oluştu.',
                'type'    => 'error',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.modals.bulk-delete-modal');
    }
}