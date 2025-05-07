<?php
namespace Modules\AI\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\AI\App\Services\AIService;
use Modules\AI\App\Models\Setting;
use Modules\AI\App\Models\Limit;
use Modules\AI\App\Models\Prompt;
use Illuminate\Support\Facades\Validator;
use App\Helpers\TenantHelpers;

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
        $settings = app(AIService::class)->getSettings();
        
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
        $tenantId = tenant_id();
        $limits = app('App\Helpers\TenantHelpers')->central(function () use ($tenantId) {
            return Limit::where('tenant_id', $tenantId)->first();
        });
        
        if ($limits) {
            $this->limits = [
                'daily_limit' => $limits->daily_limit,
                'monthly_limit' => $limits->monthly_limit,
            ];
        }
    }
    
    public function loadPrompts()
    {
        $this->prompts = app(AIService::class)->prompts()->getAllPrompts();
    }
    
    public function saveSettings()
    {
        $this->validate([
            'settings.model' => 'required|string',
            'settings.max_tokens' => 'required|integer|min:1|max:8000',
            'settings.temperature' => 'required|numeric|min:0|max:1',
            'settings.enabled' => 'boolean',
        ]);
        
        // API anahtarı boşsa ve veritabanında bir değer varsa, eski değeri koru
        if (empty($this->settings['api_key'])) {
            $currentSettings = app(AIService::class)->getSettings();
            if ($currentSettings && $currentSettings->api_key) {
                $this->settings['api_key'] = $currentSettings->api_key;
            }
        }
        
        $result = app(AIService::class)->updateSettings($this->settings);
        
        if ($result) {
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'AI ayarları güncellendi',
                'type' => 'success'
            ]);
        } else {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Ayarlar kaydedilirken bir sorun oluştu',
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
        
        $tenantId = tenant_id();
        $success = false;
        
        TenantHelpers::central(function () use ($tenantId, &$success) {
            $limit = Limit::where('tenant_id', $tenantId)->first();
            
            if (!$limit) {
                $limit = new Limit();
                $limit->tenant_id = $tenantId;
            }
            
            $limit->daily_limit = $this->limits['daily_limit'];
            $limit->monthly_limit = $this->limits['monthly_limit'];
            $success = $limit->save();
            
            return $success;
        });
        
        if ($success) {
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Kullanım limitleri güncellendi',
                'type' => 'success'
            ]);
        } else {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Limitler kaydedilirken bir sorun oluştu',
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
        
        $success = false;
        
        if ($this->editingPromptId) {
            $prompt = TenantHelpers::central(function () {
                return Prompt::find($this->editingPromptId);
            });
            
            if ($prompt) {
                $success = app(AIService::class)->prompts()->updatePrompt($prompt, $this->prompt);
                
                if ($success) {
                    $this->dispatch('toast', [
                        'title' => 'Başarılı!',
                        'message' => 'Prompt güncellendi',
                        'type' => 'success'
                    ]);
                } else {
                    $this->dispatch('toast', [
                        'title' => 'Hata!',
                        'message' => 'Prompt güncellenirken bir sorun oluştu',
                        'type' => 'error'
                    ]);
                }
            }
        } else {
            $success = app(AIService::class)->prompts()->createPrompt($this->prompt);
            
            if ($success) {
                $this->dispatch('toast', [
                    'title' => 'Başarılı!',
                    'message' => 'Yeni prompt eklendi',
                    'type' => 'success'
                ]);
            } else {
                $this->dispatch('toast', [
                    'title' => 'Hata!',
                    'message' => 'Prompt eklenirken bir sorun oluştu',
                    'type' => 'error'
                ]);
            }
        }
        
        if ($success) {
            $this->resetPromptForm();
            $this->loadPrompts();
        }
    }
    
    public function editPrompt($id)
    {
        $prompt = TenantHelpers::central(function () use ($id) {
            return Prompt::find($id);
        });
        
        if ($prompt) {
            $this->editingPromptId = $prompt->id;
            $this->prompt = [
                'name' => $prompt->name,
                'content' => $prompt->content,
                'is_default' => $prompt->is_default,
            ];
        }
    }
    
    public function deletePrompt($id)
    {
        $success = false;
        
        TenantHelpers::central(function () use ($id, &$success) {
            $prompt = Prompt::find($id);
            
            if ($prompt) {
                if ($prompt->is_default) {
                    return false;
                }
                
                $success = $prompt->delete();
            }
            
            return $success;
        });
        
        if ($success) {
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Prompt silindi',
                'type' => 'success'
            ]);
        } else {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Varsayılan prompt silinemez veya silme işlemi sırasında bir hata oluştu',
                'type' => 'error'
            ]);
        }
        
        $this->loadPrompts();
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