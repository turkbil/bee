<?php
namespace App\Livewire\Modals;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DeleteModal extends Component
{
    public $showModal = false;
    public $module;
    public $itemId;
    public $title;

    protected $listeners = ['showDeleteModal'];

    public function showDeleteModal($module, $id, $title)
    {
        $this->module    = $module;
        $this->itemId    = $id;
        $this->title     = $title;
        $this->showModal = true;
    }

    public function delete()
    {
        try {
            DB::beginTransaction();

            $tenant = tenancy()->tenant;

            if (! $tenant) {
                $this->dispatch('toast', [
                    'title'   => 'Hata!',
                    'message' => 'Tenant bilgisi bulunamadı.',
                    'type'    => 'error',
                ]);
                return;
            }

            $modelClass = "Modules\\" . ucfirst($this->module) . "\\App\\Models\\" . ucfirst($this->module);
            $item = $modelClass::where($this->module . '_id', $this->itemId)
                ->where('tenant_id', $tenant->id)
                ->first();

            if (! $item) {
                $this->dispatch('toast', [
                    'title'   => 'Hata!',
                    'message' => 'Silinmek istenen kayıt bulunamadı.',
                    'type'    => 'error',
                ]);
                return;
            }

            log_activity(
                ucfirst($this->module),
                "\"{$this->title}\" silindi.",
                $item,
                [],
                'silindi'
            );

            $item->delete();

            DB::commit();

            $this->showModal = false;

            $this->dispatch('toast', [
                'title'   => 'Silindi!',
                'message' => "\"{$this->title}\" silindi.",
                'type'    => 'danger',
            ]);

            // Ana componente silme işlemini ve silinen ID'yi bildir
            $this->dispatch('itemDeleted')->to($this->module . '-component');
            $this->dispatch('removeFromSelected', $this->itemId)->to($this->module . '-component');

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
        return view('livewire.modals.delete-modal');
    }
}