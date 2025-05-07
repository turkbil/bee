<?php
namespace Modules\AI\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\AI\App\Models\Setting;
use Modules\AI\App\Models\Limit;
use Modules\AI\App\Models\Prompt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

#[Layout('admin.layout')]
class SettingsPanel extends Component
{
    public $settings = [
        'api_key' => '',
        'model' => 'deepseek-chat',
        'max_tokens' => 4096,
        'temperature' => 0.7,
        'enabled' => true,
    ];
    
    public $limits = [
        'daily_limit' => 100,
        'monthly_limit' => 3000,
    ];
    
    public $prompt = [
        'name' => '',
        'content' => '',
        'is_default' => false,
    ];
    
    public $prompts = [];
    public $editingPromptId = null;
    
    protected $rules = [
        'settings.api_key' => 'nullable|string',
        'settings.model' => 'required|string',
        'settings.max_tokens' => 'required|integer|min:1|max:8000',
        'settings.temperature' => 'required|numeric|min:0|max:1',
        'settings.enabled' => 'boolean',
        'limits.daily_limit' => 'required|integer|min:1',
        'limits.monthly_limit' => 'required|integer|min:1',
        'prompt.name' => 'required|string|max:255',
        'prompt.content' => 'required|string',
        'prompt.is_default' => 'boolean',
    ];
    
    public function mount()
    {
        $this->loadSettings();
        $this->loadLimits();
        $this->loadPrompts();
    }
    
    public function loadSettings()
    {
        // Root tenant için ayarları yükle (tenant_id = 1)
        $settings = Setting::where('tenant_id', 1)->first();
        
        if ($settings) {
            $this->settings = [
                'api_key' => $settings->api_key,
                'model' => $settings->model,
                'max_tokens' => $settings->max_tokens,
                'temperature' => $settings->temperature,
                'enabled' => $settings->enabled,
            ];
        }
    }
    
    public function loadLimits()
    {
        // Root tenant için limitleri yükle (tenant_id = 1)
        $limits = Limit::where('tenant_id', 1)->first();
        
        if ($limits) {
            $this->limits = [
                'daily_limit' => $limits->daily_limit,
                'monthly_limit' => $limits->monthly_limit,
            ];
        }
    }
    
    public function loadPrompts()
    {
        try {
            // Root tenant için promptları yükle (tenant_id = 1)
            $this->prompts = Prompt::where('tenant_id', 1)
                ->orderBy('is_default', 'desc')
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            Log::error('Promptları yüklerken hata: ' . $e->getMessage());
            $this->prompts = [];
        }
    }
    
    public function saveSettings()
    {
        $this->validate([
            'settings.model' => 'required|string',
            'settings.max_tokens' => 'required|integer|min:1|max:8000',
            'settings.temperature' => 'required|numeric|min:0|max:1',
            'settings.enabled' => 'boolean',
        ]);
        
        try {
            // Eğer API anahtarı boşsa, mevcut değeri koru
            if (empty($this->settings['api_key'])) {
                $currentSettings = Setting::where('tenant_id', 1)->first();
                if ($currentSettings && $currentSettings->api_key) {
                    $this->settings['api_key'] = $currentSettings->api_key;
                }
            }
            
            // Root tenant için ayarları kaydet (tenant_id = 1)
            $settings = Setting::where('tenant_id', 1)->first();
            
            if (!$settings) {
                $settings = new Setting();
                $settings->tenant_id = 1;
            }
            
            $settings->api_key = $this->settings['api_key'];
            $settings->model = $this->settings['model'];
            $settings->max_tokens = $this->settings['max_tokens'];
            $settings->temperature = $this->settings['temperature'];
            $settings->enabled = $this->settings['enabled'];
            $settings->save();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'AI ayarları güncellendi',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            Log::error('Ayarlar kaydedilirken hata: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Ayarlar kaydedilirken bir sorun oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function saveLimits()
    {
        $this->validate([
            'limits.daily_limit' => 'required|integer|min:1',
            'limits.monthly_limit' => 'required|integer|min:1',
        ]);
        
        try {
            // Root tenant için limitleri kaydet (tenant_id = 1)
            $limit = Limit::where('tenant_id', 1)->first();
            
            if (!$limit) {
                $limit = new Limit();
                $limit->tenant_id = 1;
                $limit->reset_at = now();
                $limit->used_today = 0;
                $limit->used_month = 0;
            }
            
            $limit->daily_limit = $this->limits['daily_limit'];
            $limit->monthly_limit = $this->limits['monthly_limit'];
            $limit->save();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Kullanım limitleri güncellendi',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            Log::error('Limit kaydederken hata: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Limitler kaydedilirken bir sorun oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function savePrompt()
    {
        $this->validate([
            'prompt.name' => 'required|string|max:255',
            'prompt.content' => 'required|string',
            'prompt.is_default' => 'boolean',
        ]);
        
        try {
            if ($this->editingPromptId) {
                $prompt = Prompt::find($this->editingPromptId);
                
                if ($prompt) {
                    // Eğer yeni prompt varsayılan olarak işaretlendiyse, diğer varsayılanları kaldır
                    if ($this->prompt['is_default'] && !$prompt->is_default) {
                        Prompt::where('tenant_id', 1)
                            ->where('is_default', true)
                            ->update(['is_default' => false]);
                    }
                    
                    $prompt->name = $this->prompt['name'];
                    $prompt->content = $this->prompt['content'];
                    $prompt->is_default = $this->prompt['is_default'];
                    $prompt->save();
                    
                    $this->dispatch('toast', [
                        'title' => 'Başarılı!',
                        'message' => 'Prompt güncellendi',
                        'type' => 'success'
                    ]);
                }
            } else {
                // Eğer yeni prompt varsayılan olarak işaretlendiyse, diğer varsayılanları kaldır
                if ($this->prompt['is_default']) {
                    Prompt::where('tenant_id', 1)
                        ->where('is_default', true)
                        ->update(['is_default' => false]);
                }
                
                $prompt = new Prompt();
                $prompt->tenant_id = 1;
                $prompt->name = $this->prompt['name'];
                $prompt->content = $this->prompt['content'];
                $prompt->is_default = $this->prompt['is_default'];
                $prompt->save();
                
                $this->dispatch('toast', [
                    'title' => 'Başarılı!',
                    'message' => 'Yeni prompt eklendi',
                    'type' => 'success'
                ]);
            }
            
            $this->resetPromptForm();
            $this->loadPrompts();
        } catch (\Exception $e) {
            Log::error('Prompt kaydederken hata: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function editPrompt($id)
    {
        try {
            $prompt = Prompt::find($id);
            
            if ($prompt) {
                $this->editingPromptId = $prompt->id;
                $this->prompt = [
                    'name' => $prompt->name,
                    'content' => $prompt->content,
                    'is_default' => $prompt->is_default,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Prompt editlerken hata: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Prompt bilgileri yüklenirken bir sorun oluştu',
                'type' => 'error'
            ]);
        }
    }
    
    public function deletePrompt($id)
    {
        try {
            $prompt = Prompt::find($id);
            
            if ($prompt) {
                if ($prompt->is_default) {
                    $this->dispatch('toast', [
                        'title' => 'Uyarı!',
                        'message' => 'Varsayılan prompt silinemez',
                        'type' => 'warning'
                    ]);
                    return;
                }
                
                $prompt->delete();
                
                $this->dispatch('toast', [
                    'title' => 'Başarılı!',
                    'message' => 'Prompt silindi',
                    'type' => 'success'
                ]);
            }
            
            $this->loadPrompts();
        } catch (\Exception $e) {
            Log::error('Prompt silerken hata: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Silme işlemi sırasında bir hata oluştu',
                'type' => 'error'
            ]);
        }
    }
    
    public function resetPromptForm()
    {
        $this->editingPromptId = null;
        $this->prompt = [
            'name' => '',
            'content' => '',
            'is_default' => false,
        ];
    }
    
    public function render()
    {
        return view('ai::admin.livewire.settings-panel');
    }
}