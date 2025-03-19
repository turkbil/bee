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
        $this->type = $data['type'];
        $this->itemId = $data['id'];
        $this->title = $data['title'];
        if (isset($data['tenantId'])) {
            $this->tenantId = $data['tenantId'];
        }
        $this->showModal = true;
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
                return;
            }

            // Tenant verilerini kaydet
            $oldData = $tenant->toArray();
            Log::info('Tenant bulundu: ' . $tenant->title);
            
            try {
                DB::beginTransaction();
                Log::info('Transaction başlatıldı.');

                // Domain bağlantılarını sil
                $tenant->domains()->delete();
                Log::info('Tenant domainleri silindi.');
                
                // Tenant'ı sil
                $tenant->delete();
                Log::info('Tenant silindi.');
                
                // Log aktivitesi - doğrudan DB::table kullan
                DB::table('activity_log')->insert([
                    'log_name' => 'Tenant',
                    'description' => 'silindi',
                    'subject_type' => get_class($tenant),
                    'subject_id' => $tenant->id,
                    'causer_type' => auth()->check() ? get_class(auth()->user()) : null,
                    'causer_id' => auth()->id(),
                    'properties' => json_encode(['old' => $oldData]),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                Log::info('Aktivite logu manuel kaydedildi.');
                
                DB::commit();
                Log::info('Transaction commit edildi.');
                
                $this->showModal = false;
                
                $this->dispatch('toast', [
                    'title' => 'Silindi!',
                    'message' => 'Tenant başarıyla silindi.',
                    'type' => 'danger',
                ]);
                
                $this->dispatch('itemDeleted');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Tenant silme hatası: ' . $e->getMessage());
                
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
                return;
            }

            // Domain verilerini kaydet
            $oldData = $domain->toArray();
            Log::info('Domain bulundu: ' . $domain->domain);
            
            try {
                DB::beginTransaction();
                Log::info('Domain silme transaction başlatıldı.');
                
                // Domain'i sil
                $domain->delete();
                Log::info('Domain silindi.');
                
                // Log aktivitesi - doğrudan DB::table kullan
                DB::table('activity_log')->insert([
                    'log_name' => 'Domain',
                    'description' => 'silindi',
                    'subject_type' => get_class($domain),
                    'subject_id' => $domain->id,
                    'causer_type' => auth()->check() ? get_class(auth()->user()) : null,
                    'causer_id' => auth()->id(),
                    'properties' => json_encode(['old' => $oldData]),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                Log::info('Domain silme aktivite logu manuel kaydedildi.');
                
                DB::commit();
                Log::info('Domain silme transaction commit edildi.');
                
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
                DB::rollBack();
                Log::error('Domain silme hatası: ' . $e->getMessage());
                
                $this->dispatch('toast', [
                    'title' => 'Hata!',
                    'message' => 'Silme işlemi sırasında bir hata oluştu: ' . $e->getMessage(),
                    'type' => 'error',
                ]);
            }
        }
    }

    public function render()
    {
        return view('tenantmanagement::modals.delete-modal');
    }
}