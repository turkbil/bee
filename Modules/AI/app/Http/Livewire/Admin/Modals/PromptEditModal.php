<?php

namespace Modules\AI\App\Http\Livewire\Admin\Modals;

use Livewire\Component;
use Modules\AI\App\Models\Prompt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PromptEditModal extends Component
{
    public $prompt = [
        'name' => '',
        'content' => '',
        'is_default' => false,
        'is_active' => true,
    ];
    
    public $editingPromptId = null;
    public $showModal = false;
    
    protected $rules = [
        'prompt.name' => 'required|string|max:255',
        'prompt.content' => 'required|string',
        'prompt.is_default' => 'boolean',
        'prompt.is_active' => 'boolean',
    ];
    
    protected $listeners = ['openPromptModal' => 'openModal'];
    
    public function render()
    {
        return view('ai::admin.livewire.modals.prompt-edit-modal');
    }
    
    public function openModal($promptId = null)
    {
        $this->editingPromptId = $promptId;
        $this->resetForm();
        
        if ($promptId) {
            $this->loadPrompt($promptId);
        }
        
        $this->showModal = true;
    }
    
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }
    
    public function resetForm()
    {
        $this->prompt = [
            'name' => '',
            'content' => '',
            'is_default' => false,
            'is_active' => true,
        ];
        $this->editingPromptId = null;
        $this->resetValidation();
    }
    
    public function loadPrompt($promptId)
    {
        try {
            $prompt = Prompt::find($promptId);
            
            if ($prompt) {
                $this->prompt = [
                    'name' => $prompt->name,
                    'content' => $prompt->content,
                    'is_default' => $prompt->is_default,
                    'is_active' => $prompt->is_active,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Prompt yüklenirken hata: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Prompt bilgileri yüklenemedi',
                'type' => 'error'
            ]);
        }
    }
    
    public function save()
    {
        $this->validate();
        
        try {
            if ($this->editingPromptId) {
                // Güncelleme
                $prompt = Prompt::find($this->editingPromptId);
                
                if (!$prompt) {
                    throw new \Exception('Güncellenecek prompt bulunamadı');
                }
                
                // Sistem promptlarını koruma
                if ($prompt->is_system && !$prompt->is_common) {
                    throw new \Exception('Sistem promptları düzenlenemez');
                }
                
            } else {
                // Yeni oluşturma
                $prompt = new Prompt();
                $prompt->is_system = false;
                $prompt->is_common = false;
            }
            
            // Eğer bu prompt varsayılan yapılacaksa, diğerlerini kaldır
            if ($this->prompt['is_default']) {
                Prompt::where('is_default', true)->update(['is_default' => false]);
            }
            
            $prompt->name = $this->prompt['name'];
            $prompt->content = $this->prompt['content'];
            $prompt->is_default = $this->prompt['is_default'];
            $prompt->is_active = $this->prompt['is_active'];
            $prompt->save();
            
            // Önbelleği temizle
            Cache::forget("ai_prompts");
            Cache::forget("ai_default_prompt");
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => $this->editingPromptId ? 'Prompt güncellendi' : 'Prompt oluşturuldu',
                'type' => 'success'
            ]);
            
            $this->dispatch('promptSaved');
            $this->closeModal();
            
        } catch (\Exception $e) {
            Log::error('Prompt kaydedilirken hata: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
}