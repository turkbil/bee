<?php

namespace Modules\AI\App\Http\Livewire\Admin\Settings\Modals;

use Livewire\Component;
use Modules\AI\App\Models\Prompt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PromptDeleteModal extends Component
{
    public $prompt;
    public $showModal = false;
    
    protected $listeners = ['openPromptDeleteModal' => 'openModal'];
    
    public function render()
    {
        return view('ai::admin.settings.modals.prompt-delete-modal');
    }
    
    public function openModal($promptId)
    {
        try {
            $this->prompt = Prompt::find($promptId);
            
            if (!$this->prompt) {
                throw new \Exception('Prompt bulunamadı');
            }
            
            $this->showModal = true;
        } catch (\Exception $e) {
            Log::error('Prompt silme modalı açılırken hata: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Prompt bulunamadı',
                'type' => 'error'
            ]);
        }
    }
    
    public function closeModal()
    {
        $this->showModal = false;
        $this->prompt = null;
    }
    
    public function confirmDelete()
    {
        try {
            if (!$this->prompt) {
                throw new \Exception('Silinecek prompt bulunamadı');
            }
            
            // Sistem kontrolleri
            if ($this->prompt->is_default) {
                throw new \Exception('Varsayılan prompt silinemez');
            }
            
            if ($this->prompt->is_system) {
                throw new \Exception('Sistem promptları silinemez');
            }
            
            if ($this->prompt->is_common) {
                throw new \Exception('Ortak özellikler promptu silinemez');
            }
            
            $promptName = $this->prompt->name;
            $this->prompt->delete();
            
            // Önbelleği temizle
            Cache::forget("ai_prompts");
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => "\"{$promptName}\" promptu silindi",
                'type' => 'success'
            ]);
            
            $this->dispatch('promptSaved'); // Listeyi yenile
            $this->closeModal();
            
        } catch (\Exception $e) {
            Log::error('Prompt silinirken hata: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
}