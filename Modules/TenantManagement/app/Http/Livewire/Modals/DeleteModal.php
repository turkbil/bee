<?php
namespace Modules\TenantManagement\App\Http\Livewire\Modals;

use Livewire\Component;
use App\Models\Tenant;
use Stancl\Tenancy\Database\Models\Domain;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteModal extends Component
{
    public $showModal = false;
    public $itemId;
    public $type;
    public $title;
    public $tenantId = null;

    protected $listeners = ['showDeleteModal'];

    // DÜZELTME: Livewire 3'e uygun parametre işleme
    public function handleShowDeleteModal($params)
    {
        // Eski sürüm uyumluluğu için (parametre string gelirse decode et)
        if (is_string($params)) {
            $params = json_decode($params, true);
        }

        $this->type = $params['type'];
        $this->itemId = $params['id'];
        $this->title = $params['title'];
        $this->tenantId = $params['tenantId'] ?? null;
        $this->showModal = true;
    }

    public function delete()
    {
        try {
            DB::transaction(function () {
                if ($this->type === 'tenant') {
                    $tenant = Tenant::findOrFail($this->itemId);
                    $tenant->domains()->delete();
                    $tenant->delete();

                    $this->dispatch('toast', [
                        'title' => 'Başarılı!',
                        'message' => 'Tenant silindi.',
                        'type' => 'success'
                    ]);

                } elseif ($this->type === 'domain') {
                    $domain = Domain::findOrFail($this->itemId);
                    $domain->delete();

                    $this->dispatch('toast', [
                        'title' => 'Başarılı!',
                        'message' => 'Domain silindi.',
                        'type' => 'success'
                    ]);
                }
            });

            $this->closeModal();
            $this->dispatch('itemDeleted');

        } catch (\Exception $e) {
            Log::error("Silme hatası: " . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Silme işlemi başarısız: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function closeModal()
    {
        $this->reset(['showModal', 'itemId', 'type', 'title', 'tenantId']);
    }

    public function render()
    {
        return view('tenantmanagement::modals.delete-modal');
    }
}