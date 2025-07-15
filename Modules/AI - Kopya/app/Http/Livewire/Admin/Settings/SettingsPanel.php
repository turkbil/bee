<?php

namespace Modules\AI\App\Http\Livewire\Admin\Settings;

use Livewire\Component;
use Modules\AI\App\Models\Setting;
use Modules\AI\App\Models\Prompt;
use Modules\AI\App\Services\DeepSeekService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Helpers\TenantHelpers;

class SettingsPanel extends Component
{
    public $settings = [
        'api_key' => '',
        'model' => 'deepseek-chat',
        'max_tokens' => 4096,
        'temperature' => 0.7,
        'enabled' => true,
    ];
    
    public $prompt = [
        'name' => '',
        'content' => '',
        'is_default' => false,
        'is_common' => false,
    ];
    
    public $commonPrompt = [
        'content' => '',
    ];
    
    public $prompts = [];
    public $editingPromptId = null;
    public $isTestingConnection = false;
    public $connectionTestResult = null;
    
    protected $rules = [
        'settings.api_key' => 'nullable|string',
        'settings.model' => 'required|string',
        'settings.max_tokens' => 'required|integer|min:1|max:8000',
        'settings.temperature' => 'required|numeric|min:0|max:1',
        'settings.enabled' => 'boolean',
        'prompt.name' => 'required|string|max:255',
        'prompt.content' => 'required|string',
        'prompt.is_default' => 'boolean',
        'prompt.is_common' => 'boolean',
        'commonPrompt.content' => 'required|string',
    ];
    
    protected function getListeners()
    {
        return [
            'promptSaved' => 'loadPrompts',
        ];
    }
    
    public function mount()
    {
        $this->loadSettings();
        $this->loadPrompts();
        $this->loadCommonPrompt();
    }
    
    public function render()
    {
        return view('ai::admin.settings.settings-panel');
    }
    
    public function loadSettings()
    {
        $settings = Setting::first();
        
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
    
    public function loadPrompts()
    {
        try {
            $this->prompts = Prompt::orderBy('is_default', 'desc')
                ->orderBy('is_common', 'desc')
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            Log::error('Promptları yüklerken hata: ' . $e->getMessage());
            $this->prompts = [];
        }
    }
    
    public function loadCommonPrompt()
    {
        try {
            $commonPrompt = Prompt::where('is_common', true)->first();
            
            if ($commonPrompt) {
                $this->commonPrompt = [
                    'content' => $commonPrompt->content,
                ];
            } else {
                $this->commonPrompt = [
                    'content' => 'Adın WindsurfAI ve sen yazılım geliştirme ekibimizin yapay zeka asistanısın. Türkçe yanıt verirsin ve Laravel, JavaScript, PHP konularında uzmansın. Acele etmez, düşünerek yanıt verirsin. Belgelere bağlı kalarak dogmatik değil pratik çözümler üretirsin. Şirketimizin adı Windsurf Teknoloji ve 2023 yılında kurulmuştur.',
                ];
            }
        } catch (\Exception $e) {
            Log::error('Ortak özellikler promptunu yüklerken hata: ' . $e->getMessage());
            $this->commonPrompt = [
                'content' => 'Adın WindsurfAI ve sen yazılım geliştirme ekibimizin yapay zeka asistanısın. Türkçe yanıt verirsin.',
            ];
        }
    }
    
    public function testApiConnection()
    {
        $this->isTestingConnection = true;
        $this->connectionTestResult = null;
        
        try {
            // API anahtarını doğrula
            if (empty($this->settings['api_key'])) {
                $this->connectionTestResult = [
                    'success' => false,
                    'message' => 'API anahtarı boş olamaz!'
                ];
                $this->isTestingConnection = false;
                return;
            }
            
            // DeepSeek servisi oluştur ve bağlantıyı test et
            $deepSeekService = new DeepSeekService();
            $deepSeekService->setApiKey($this->settings['api_key']);
            
            $result = $deepSeekService->testConnection();
            
            $this->connectionTestResult = [
                'success' => $result,
                'message' => $result 
                    ? 'API bağlantısı başarılı!' 
                    : 'API bağlantısı başarısız. Lütfen API anahtarınızı kontrol edin.'
            ];
            
            $this->dispatch('toast', [
                'title' => $result ? 'Başarılı!' : 'Hata!',
                'message' => $this->connectionTestResult['message'],
                'type' => $result ? 'success' : 'error'
            ]);
        } catch (\Exception $e) {
            $this->connectionTestResult = [
                'success' => false,
                'message' => 'Bağlantı testi sırasında hata oluştu: ' . $e->getMessage()
            ];
            
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => $this->connectionTestResult['message'],
                'type' => 'error'
            ]);
            
            Log::error('API bağlantı testi hatası: ' . $e->getMessage());
        }
        
        $this->isTestingConnection = false;
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
                $currentSettings = Setting::first();
                if ($currentSettings && $currentSettings->api_key) {
                    $this->settings['api_key'] = $currentSettings->api_key;
                }
            }
            
            $settings = Setting::first();
            
            if (!$settings) {
                $settings = new Setting();
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
    
    public function saveCommonPrompt()
    {
        $this->validate([
            'commonPrompt.content' => 'required|string',
        ]);
        
        try {
            $commonPrompt = Prompt::where('is_common', true)->first();
            
            if (!$commonPrompt) {
                $commonPrompt = new Prompt();
                $commonPrompt->name = 'Ortak Özellikler';
                $commonPrompt->is_default = false;
                $commonPrompt->is_system = true;
                $commonPrompt->is_common = true;
            }
            
            $commonPrompt->content = $this->commonPrompt['content'];
            $commonPrompt->save();
            
            // Önbelleği temizle
            Cache::forget("ai_common_prompt");
            Cache::forget("ai_prompts");
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Ortak özellikler güncellendi',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            Log::error('Ortak özellikler kaydedilirken hata: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Ortak özellikler kaydedilirken bir sorun oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function togglePromptActive($id)
    {
        try {
            $prompt = Prompt::find($id);
            
            if (!$prompt) {
                throw new \Exception('Prompt bulunamadı');
            }
            
            if ($prompt->is_system && !$prompt->is_common) {
                throw new \Exception('Sistem promptlarının durumu değiştirilemez');
            }
            
            $prompt->is_active = !$prompt->is_active;
            $prompt->save();
            
            // Önbelleği temizle
            Cache::forget("ai_prompts");
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Prompt durumu ' . ($prompt->is_active ? 'aktif' : 'pasif') . ' olarak güncellendi',
                'type' => 'success'
            ]);
            
            $this->loadPrompts();
        } catch (\Exception $e) {
            Log::error('Prompt durumu değiştirilirken hata: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function makeDefault($id)
    {
        try {
            $prompt = Prompt::find($id);
            
            if (!$prompt) {
                throw new \Exception('Prompt bulunamadı');
            }
            
            if ($prompt->is_default) {
                return;
            }
            
            // Önceki varsayılan promptu devre dışı bırak
            Prompt::where('is_default', true)->update(['is_default' => false]);
            
            // Yeni promptu varsayılan yap
            $prompt->is_default = true;
            $prompt->save();
            
            // Önbelleği temizle
            Cache::forget("ai_default_prompt");
            Cache::forget("ai_prompts");
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => '"' . $prompt->name . '" varsayılan prompt olarak ayarlandı',
                'type' => 'success'
            ]);
            
            $this->loadPrompts();
        } catch (\Exception $e) {
            Log::error('Varsayılan prompt ayarlanırken hata: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }    
    
    public function editPrompt($id)
    {
        try {
            $this->dispatch('openPromptModal', $id);
        } catch (\Exception $e) {
            Log::error('Prompt editlerken hata: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Prompt bilgileri yüklenirken bir sorun oluştu',
                'type' => 'error'
            ]);
        }
    }
    
    public function resetPromptForm()
    {
        $this->dispatch('openPromptModal');
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
                
                if ($prompt->is_system) {
                    $this->dispatch('toast', [
                        'title' => 'Uyarı!',
                        'message' => 'Sistem promptları silinemez',
                        'type' => 'warning'
                    ]);
                    return;
                }
                
                if ($prompt->is_common) {
                    $this->dispatch('toast', [
                        'title' => 'Uyarı!',
                        'message' => 'Ortak özellikler promptu silinemez',
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
}