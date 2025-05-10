<?php

namespace Modules\AI\App\Http\Livewire\Admin\Modals;

use Livewire\Component;
use Modules\AI\App\Models\Prompt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PromptDeleteModal extends Component
{
    public $showModal = false;
    public $promptId = null;
    public $promptName = '';
    
    protected $listeners = [
        'showPromptDeleteModal' => 'openModal',
        'hidePromptDeleteModal' => 'closeModal',
    ];
    
    public function openModal($data = [])
    {
        $this->promptId = $data['id'] ?? null;
        $this->promptName = $data['name'] ?? '';
        $this->showModal = true;
    }
    
    public function closeModal()
    {
        $this->showModal = false;
    }
    
    public function delete()
    {
        try {
            $prompt = Prompt::find($this->promptId);
            
            if (!$prompt) {
                throw new \Exception('Prompt bulunamadı');
            }
            
            if ($prompt->is_system) {
                throw new \Exception('Sistem promptları silinemez');
            }
            
            if ($prompt->is_common) {
                throw new \Exception('Ortak özellikler promptu silinemez');
            }
            
            if ($prompt->is_default) {
                throw new \Exception('Varsayılan prompt silinemez');
            }
            
            $prompt->delete();
            
            // Önbelleği temizle
            Cache::forget("ai_prompts");
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Prompt silindi',
                'type' => 'success'
            ]);
            
            $this->closeModal();
            $this->dispatch('refreshPrompts');
            
        } catch (\Exception $e) {
            Log::error('Prompt silerken hata: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function render()
    {
        return view('ai::admin.livewire.modals.prompt-delete-modal');
    }
}