<?php
namespace App\Livewire\Modals;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\User;

class UserDeleteModal extends Component
{
    public $showModal = false;
    public $userId;
    public $userName;

    protected $listeners = ['showDeleteModal'];

    public function showDeleteModal($userId, $userName)
    {
        $this->userId    = $userId;
        $this->userName  = $userName;
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
    
            // Kullanıcı silme işlemi
            $user = User::where('id', $this->userId)
                ->where('tenant_id', $tenant->id)
                ->first();
    
            if (! $user) {
                $this->dispatch('toast', [
                    'title'   => 'Hata!',
                    'message' => "ID: {$this->userId} ile eşleşen kullanıcı bulunamadı veya tenant bilgisi uyuşmuyor.",
                    'type'    => 'error',
                ]);
                return;
            }
    
            // Loglama
            log_activity(
                'Kullanıcı',
                "\"{$this->userName}\" kullanıcısı silindi.",
                $user,
                [],
                'silindi'
            );
    
            // Kullanıcıyı sil
            $user->delete();
    
            DB::commit();
    
            // Modal'ı kapat
            $this->showModal = false;
    
            $this->dispatch('toast', [
                'title'   => 'Silindi!',
                'message' => "\"{$this->userName}\" kullanıcısı silindi.",
                'type'    => 'danger',
            ]);
    
            // Ana componente silme işlemini ve silinen ID'yi bildir
            $this->dispatch('itemDeleted', $this->userId)->to('user-component');
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            $this->dispatch('toast', [
                'title'   => 'Hata!',
                'message' => 'Silme işlemi sırasında bir hata oluştu: ' . $e->getMessage(),
                'type'    => 'error',
            ]);
        }
    }
    
    public function render()
    {
        return view('livewire.modals.user-delete-modal');
    }
}