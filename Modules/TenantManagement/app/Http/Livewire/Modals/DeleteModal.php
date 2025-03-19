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
    public $type; // tenant, domain
    public $title;
    public $tenantId = null; // Domain silinince tenant'ı güncellemek için

    protected $listeners = ['showDeleteModal'];

    public function showDeleteModal($data)
    {
        $this->type = $data['type'] ?? '';
        $this->itemId = $data['id'] ?? null;
        $this->title = $data['title'] ?? '';
        
        if (isset($data['tenantId'])) {
            $this->tenantId = $data['tenantId'];
        }
        
        $this->showModal = true;
        
        Log::info('Delete Modal Açıldı', [
            'type' => $this->type,
            'id' => $this->itemId,
            'title' => $this->title
        ]);
    }

    public function delete()
    {
        Log::info('Silme işlemi başlıyor. Tip: ' . $this->type . ', ID: ' . $this->itemId);
        
        if ($this->type === 'tenant') {
            $tenant = Tenant::find($this->itemId);
            
            if (!$tenant) {
                $this->dispatch('toast', [
                    'title' => 'Hata!',
                    'message' => 'Silinmek istenen tenant bulunamadı.',
                    'type' => 'error',
                ]);
                $this->showModal = false;
                return;
            }

            try {
                // Tenant verilerini al
                $oldData = $tenant->toArray();
                
                // Domain bağlantılarını sil
                $tenant->domains()->delete();
                
                // Tenant'ı sil
                $tenant->delete();
                
                // Log ekle
                activity()
                    ->performedOn($tenant)
                    ->causedBy(auth()->user())
                    ->inLog(class_basename($tenant))
                    ->withProperties(['old' => $oldData])
                    ->log("\"" . ($tenant->title ?? 'Tenant') . "\" silindi");
                
                $this->showModal = false;
                
                $this->dispatch('toast', [
                    'title' => 'Silindi!',
                    'message' => 'Tenant başarıyla silindi.',
                    'type' => 'danger',
                ]);
                
                $this->dispatch('itemDeleted');
            } catch (\Exception $e) {
                Log::error('Tenant silme hatası: ' . $e->getMessage());
                
                $this->showModal = false;
                
                $this->dispatch('toast', [
                    'title' => 'Hata!',
                    'message' => 'Silme işlemi sırasında bir hata oluştu: ' . $e->getMessage(),
                    'type' => 'error',
                ]);
            }
        } elseif ($this->type === 'domain') {
            $domain = Domain::find($this->itemId);
            
            if (!$domain) {
                $this->dispatch('toast', [
                    'title' => 'Hata!',
                    'message' => 'Silinmek istenen domain bulunamadı.',
                    'type' => 'error',
                ]);
                $this->showModal = false;
                return;
            }

            try {
                // Domain verilerini al
                $oldData = $domain->toArray();
                
                // Domain'i sil
                $domain->delete();
                
                // Log ekle
                activity()
                    ->performedOn($domain)
                    ->causedBy(auth()->user())
                    ->inLog(class_basename($domain))
                    ->withProperties(['old' => $oldData])
                    ->log("\"" . ($domain->domain ?? 'Domain') . "\" silindi");
                
                $this->showModal = false;
                
                $this->dispatch('toast', [
                    'title' => 'Silindi!',
                    'message' => 'Domain başarıyla silindi.',
                    'type' => 'danger',
                ]);
                
                // Domain listesini güncellemek için tenantId gönderilmişse
                if ($this->tenantId) {
                    $this->dispatch('refreshDomains', $this->tenantId);
                }
                
                $this->dispatch('itemDeleted');
            } catch (\Exception $e) {
                Log::error('Domain silme hatası: ' . $e->getMessage());
                
                $this->showModal = false;
                
                $this->dispatch('toast', [
                    'title' => 'Hata!',
                    'message' => 'Silme işlemi sırasında bir hata oluştu: ' . $e->getMessage(),
                    'type' => 'error',
                ]);
            }
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        Log::info('Modal kapatıldı');
    }

    public function render()
    {
        return view('tenantmanagement::modals.delete-modal', [
            'showModal' => $this->showModal,
            'title' => $this->title
        ]);
    }
}