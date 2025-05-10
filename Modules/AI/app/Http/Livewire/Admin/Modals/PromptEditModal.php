<?php

namespace Modules\AI\App\Http\Livewire\Admin\Modals;

use Livewire\Component;
use Modules\AI\App\Models\Prompt;
use Illuminate\Support\Facades\Log;

class PromptEditModal extends Component
{
    public $prompt = [
        'id' => null,
        'name' => '',
        'content' => '',
        'is_default' => false,
        'is_common' => false,
    ];
    
    public $showModal = false;
    public $isEditing = false;
    
    protected $listeners = [
        'openPromptModal' => 'openModal',
        'closePromptModal' => 'closeModal'
    ];
    
    protected $rules = [
        'prompt.name' => 'required|string|max:255',
        'prompt.content' => 'required|string',
        'prompt.is_default' => 'boolean',
        'prompt.is_common' => 'boolean',
    ];
    
    public function openModal($promptId = null)
    {
        $this->resetValidation();
        
        if ($promptId) {
            $this->isEditing = true;
            $promptModel = Prompt::find($promptId);
            
            if ($promptModel) {
                $this->prompt = [
                    'id' => $promptModel->id,
                    'name' => $promptModel->name,
                    'content' => $promptModel->content,
                    'is_default' => $promptModel->is_default,
                    'is_common' => $promptModel->is_common,
                ];
            }
        } else {
            $this->isEditing = false;
            $this->prompt = [
                'id' => null,
                'name' => '',
                'content' => '',
                'is_default' => false,
                'is_common' => false,
            ];
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
            if ($this->isEditing) {
                $promptModel = Prompt::find($this->prompt['id']);
                
                if (!$promptModel) {
                    $this->dispatch('toast', [
                        'title' => 'Hata!',
                        'message' => 'Düzenlenecek prompt bulunamadı.',
                        'type' => 'error'
                    ]);
                    return;
                }
                
                // Sistem promptu kontrolü
                if ($promptModel->is_system && !$promptModel->is_common) {
                    $this->dispatch('toast', [
                        'title' => 'Uyarı!',
                        'message' => 'Sistem promptları düzenlenemez',
                        'type' => 'warning'
                    ]);
                    return;
                }
                
                // Eğer yeni prompt varsayılan olarak işaretlendiyse, diğer varsayılanları kaldır
                if ($this->prompt['is_default'] && !$promptModel->is_default) {
                    Prompt::where('is_default', true)
                        ->update(['is_default' => false]);
                }
                
                // Eğer yeni prompt ortak özellikler olarak işaretlendiyse, diğer ortak özellikleri kaldır
                if ($this->prompt['is_common'] && !$promptModel->is_common) {
                    Prompt::where('is_common', true)
                        ->update(['is_common' => false]);
                }
                
                $promptModel->name = $this->prompt['name'];
                $promptModel->content = $this->prompt['content'];
                $promptModel->is_default = $this->prompt['is_default'];
                $promptModel->is_common = $this->prompt['is_common'];
                $promptModel->save();
                
                $this->dispatch('toast', [
                    'title' => 'Başarılı!',
                    'message' => 'Prompt başarıyla güncellendi.',
                    'type' => 'success'
                ]);
            } else {
                // Eğer yeni prompt varsayılan olarak işaretlendiyse, diğer varsayılanları kaldır
                if ($this->prompt['is_default']) {
                    Prompt::where('is_default', true)
                        ->update(['is_default' => false]);
                }
                
                // Eğer yeni prompt ortak özellikler olarak işaretlendiyse, diğer ortak özellikleri kaldır
                if ($this->prompt['is_common']) {
                    Prompt::where('is_common', true)
                        ->update(['is_common' => false]);
                }
                
                $promptModel = new Prompt();
                $promptModel->name = $this->prompt['name'];
                $promptModel->content = $this->prompt['content'];
                $promptModel->is_default = $this->prompt['is_default'];
                $promptModel->is_common = $this->prompt['is_common'];
                $promptModel->is_system = false; // Yeni eklenen promptlar her zaman özel (sistem değil)
                $promptModel->save();
                
                $this->dispatch('toast', [
                    'title' => 'Başarılı!',
                    'message' => 'Yeni prompt eklendi.',
                    'type' => 'success'
                ]);
            }
            
            // Önbelleği temizleme için event gönder
            $this->dispatch('promptSaved');
            
            // Modal'ı kapat
            $this->closeModal();
        } catch (\Exception $e) {
            Log::error('Prompt kaydederken hata: ' . $e->getMessage());
            
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