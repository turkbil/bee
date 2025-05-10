<?php

namespace Modules\AI\App\Http\Livewire\Modals;

use Livewire\Component;
use Modules\AI\App\Models\Prompt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PromptEditModal extends Component
{
    public $showModal = false;
    public $promptId = null;
    public $prompt = [
        'name' => '',
        'content' => '',
        'is_default' => false,
    ];
    
    protected $listeners = [
        'showPromptEditModal' => 'openModal',
        'hidePromptEditModal' => 'closeModal',
    ];
    
    protected $rules = [
        'prompt.name' => 'required|string|max:255',
        'prompt.content' => 'required|string',
        'prompt.is_default' => 'boolean',
    ];
    
    protected $messages = [
        'prompt.name.required' => 'Prompt adı gereklidir',
        'prompt.content.required' => 'Prompt içeriği gereklidir',
    ];
    
    public function openModal($data = [])
    {
        $this->resetValidation();
        $this->resetExcept('showModal');
        
        if (isset($data['id'])) {
            $this->promptId = $data['id'];
            $promptModel = Prompt::find($this->promptId);
            
            if ($promptModel) {
                $this->prompt = [
                    'name' => $promptModel->name,
                    'content' => $promptModel->content,
                    'is_default' => $promptModel->is_default,
                ];
            }
        }
        
        $this->showModal = true;
    }
    
    public function closeModal()
    {
        $this->showModal = false;
    }
    
    public function save()
    {
        $this->validate();
        
        try {
            if ($this->promptId) {
                $promptModel = Prompt::find($this->promptId);
                
                if (!$promptModel) {
                    throw new \Exception('Prompt bulunamadı');
                }
                
                // Eğer yeni prompt varsayılan olarak işaretlendiyse, diğer varsayılanları kaldır
                if ($this->prompt['is_default'] && !$promptModel->is_default) {
                    Prompt::where('is_default', true)
                        ->update(['is_default' => false]);
                }
                
                $promptModel->update([
                    'name' => $this->prompt['name'],
                    'content' => $this->prompt['content'],
                    'is_default' => $this->prompt['is_default'],
                ]);
                
                // Önbelleği temizle
                Cache::forget("ai_prompts");
                Cache::forget("ai_default_prompt");
                
                $this->dispatch('toast', [
                    'title' => 'Başarılı!',
                    'message' => 'Prompt güncellendi',
                    'type' => 'success'
                ]);
            } else {
                // Eğer yeni prompt varsayılan olarak işaretlendiyse, diğer varsayılanları kaldır
                if ($this->prompt['is_default']) {
                    Prompt::where('is_default', true)
                        ->update(['is_default' => false]);
                }
                
                Prompt::create([
                    'name' => $this->prompt['name'],
                    'content' => $this->prompt['content'],
                    'is_default' => $this->prompt['is_default'],
                    'is_system' => false,
                    'is_common' => false,
                ]);
                
                // Önbelleği temizle
                Cache::forget("ai_prompts");
                Cache::forget("ai_default_prompt");
                
                $this->dispatch('toast', [
                    'title' => 'Başarılı!',
                    'message' => 'Yeni prompt eklendi',
                    'type' => 'success'
                ]);
            }
            
            $this->closeModal();
            $this->dispatch('refreshPrompts');
            
        } catch (\Exception $e) {
            Log::error('Prompt kaydederken hata: ' . $e->getMessage(), ['exception' => $e]);
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function render()
    {
        return view('ai::admin.livewire.modals.prompt-edit-modal');
    }
}