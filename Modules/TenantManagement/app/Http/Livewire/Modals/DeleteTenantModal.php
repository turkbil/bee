<?php
namespace Modules\TenantManagement\App\Http\Livewire\Modals;

use Livewire\Component;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteTenantModal extends Component
{
    public $showModal = false;
    public $tenantId;
    public $tenantTitle;

    protected $listeners = ['showDeleteTenantModal'];

    public function showDeleteTenantModal($data)
    {
        $this->tenantId = is_array($data) ? ($data['id'] ?? null) : null;
        $this->tenantTitle = is_array($data) ? ($data['title'] ?? 'Adsız Tenant') : 'Adsız Tenant';
        
        if (!$this->tenantId) {
            Log::error('DeleteTenantModal hatalı parametre formatı', ['data' => $data]);
            return;
        }
        
        $this->showModal = true;
    }

    public function delete()
    {
        try {
            DB::beginTransaction();

            $tenant = Tenant::findOrFail($this->tenantId);
            
            // Tenant ile ilişkili domain'leri sil
            $tenant->domains()->delete();
            
            // Activity log kaydı
            activity()
                ->performedOn($tenant)
                ->causedBy(auth()->user())
                ->log("silindi");
                
            // Tenant'ı sil
            $tenant->delete();

            DB::commit();
            
            $this->showModal = false;
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Tenant başarıyla silindi.',
                'type' => 'success'
            ]);
            
            $this->dispatch('refreshList');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Tenant silme hatası: " . $e->getMessage());
            
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Silme işlemi sırasında bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function closeModal()
    {
        $this->reset(['showModal', 'tenantId', 'tenantTitle']);
    }

    public function render()
    {
        return view('tenantmanagement::modals.delete-tenant-modal');
    }
}