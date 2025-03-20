<?php
namespace Modules\TenantManagement\App\Http\Livewire\Modals;

use Livewire\Component;
use Stancl\Tenancy\Database\Models\Domain;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteDomainModal extends Component
{
    public $showModal = false;
    public $domainId;
    public $domainName;
    public $tenantId;

    protected $listeners = ['showDeleteDomainModal'];

    public function showDeleteDomainModal($domainId, $domainName, $tenantId)
    {
        // Parametre tipi kontrolü
        if (!is_string($domainId) && !is_int($domainId)) {
            Log::error("Domain silme hatası: Geçersiz domainId tipi", ['domainId' => $domainId]);
            return;
        }
        
        $this->domainId = $domainId;
        $this->domainName = $domainName;
        $this->tenantId = $tenantId;
        $this->showModal = true;
    }

    public function delete()
    {
        try {
            DB::beginTransaction();

            $domain = Domain::findOrFail($this->domainId);
            
            // Activity log kaydı
            activity()
                ->performedOn($domain)
                ->causedBy(auth()->user())
                ->log("silindi");
                
            // Domain'i sil
            $domain->delete();

            DB::commit();
            
            $this->showModal = false;
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Domain başarıyla silindi.',
                'type' => 'success'
            ]);
            
            $this->dispatch('refreshDomains', $this->tenantId);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Domain silme hatası: " . $e->getMessage());
            
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Silme işlemi sırasında bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function closeModal()
    {
        $this->reset(['showModal', 'domainId', 'domainName', 'tenantId']);
    }

    public function render()
    {
        return view('tenantmanagement::modals.delete-domain-modal');
    }
}